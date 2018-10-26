package ru.devprom.items;

import java.util.Arrays;

public final class TimetableItem implements Comparable<TimetableItem> {
     private  String name; 
     private  String sum; 
     private  int daysCount;
     private  String[] days;
    	
    	public TimetableItem (String name, String[] days, String sum){
    		this.name=name;
    		this.days=days;
    		this.sum=sum;
    		this.daysCount = days.length;
    	}
    	
    	
    	
   	    @Override
		public String toString() {
			return "TimetableItem [name=" + name + ", sum=" + sum
					+ ", daysCount=" + daysCount + ", days="
					+ Arrays.toString(days) + "]";
		}

        

		@Override
		public int hashCode() {
			final int prime = 31;
			int result = 1;
			result = prime * result + Arrays.hashCode(days);
			result = prime * result + daysCount;
			result = prime * result + ((name == null) ? 0 : name.hashCode());
			result = prime * result + ((sum == null) ? 0 : sum.hashCode());
			return result;
		}



		@Override
		public boolean equals(Object obj) {
			if (this == obj)
				return true;
			if (obj == null)
				return false;
			if (getClass() != obj.getClass())
				return false;
			TimetableItem other = (TimetableItem) obj;
			if (!Arrays.equals(days, other.days))
				return false;
			if (daysCount != other.daysCount)
				return false;
			if (name == null) {
				if (other.name != null)
					return false;
			} else if (!name.split("]")[0].equalsIgnoreCase(other.name.split("]")[0]))
				return false;
			if (sum == null) {
				if (other.sum != null)
					return false;
			} else if (!sum.equals(other.sum))
				return false;
			return true;
		}



		public String getName() {
			return name;
		}

		public String getSum() {
			return sum;
		}
		
		public int getDaysCount() {
			return daysCount;
		}
		
		public String[] getDays() {
			return days;
		}

		@Override
		public int compareTo(TimetableItem o) {
			return this.getName().hashCode() - o.getName().hashCode();
		}
    	
}
